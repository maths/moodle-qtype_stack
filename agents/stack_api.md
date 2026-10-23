# Setting up and using the STACK API

**Audience: the assistant. How to run STACK questions outside Moodle while authoring them.**

The STACK API is STACK's own code as a stateless web service. It takes the
Moodle XML of one question in each request, so it needs no Moodle, no database
and no import, and it answers in seconds. Use it to run a question's tests,
look at its variants and try answers while writing the XML. The essentials
file (`agents/stack_essentials.md`, "Run the tests through the STACK API") says
why and when; this file says how. The developer documentation for every route
is `api/README.md` in the STACK source.

## Set it up

The API runs as two Docker containers: `stackmaths/stackapi` (PHP, STACK) and
`mathinstitut/goemaxima` (Maxima with STACK's libraries).

**Every version number must be the target site's STACK version**: the number
in the site's `question/type/stack/version.php` (`$plugin->version`), which is
also the question's `<stackversion>`. For example, STACK 4.13.1 is
`2026080600`. Four places carry it, and they must agree:

1. the `goemaxima` image tag, `mathinstitut/goemaxima:<version>-latest`;
2. the `stackapi` image: a published tag, or one built from the same release;
3. `$CFG->stackmaximaversion` in the API's `api/config.php`;
4. `$CFG->version` in the same file.

A mismatch tests different code from the site's: random variants and marking
can differ, so tests fail that pass on the site, or pass when they should not.

**Keep the number in one place.** Set it once, as the environment variable
`STACK_VERSION`, and let everything else read it: the compose file below takes
both image tags from it, and the configuration takes both version lines from
it. Moving to a new STACK release is then one change. Do not copy a number
from these instructions: take it from the site.

**The `goemaxima` image** is published for every STACK release. **The
`stackapi` image** is published for each release by a release workflow, but
not for some older releases: check the tags of `stackmaths/stackapi` on Docker
Hub. When there is none for the target version,
build it from STACK's source at the site's release tag. Set `RELEASE` to that
tag, for example `v4.13.1` for a site on STACK 4.13.1:

    RELEASE=v4.13.1
    git clone --depth 1 --branch "$RELEASE" \
        https://github.com/maths/moodle-qtype_stack.git stack-src
    STACK_VERSION=$(sed -n 's/^\$plugin->version *= *\([0-9]*\);.*/\1/p' stack-src/version.php)
    echo "$STACK_VERSION"
    cp stack-src/api/config_sample.txt stack-src/api/config.php
    docker build -f stack-src/api/docker/Dockerfile --target production \
        -t stackapi-local:"$STACK_VERSION" stack-src

Check that the number printed is the site's `$plugin->version`, for example
`2026080600` for `v4.13.1`. If not, the tag is wrong. The build needs
`api/config.php` in the source: without it the build fails at the step that
installs language packs. The build takes several minutes and needs a network
connection. The image is local to the machine.

**Configuration.** Copy `api/config_sample.txt` to `config.php` and mount it
into the container at `/srv/stack/api/config.php`. If the sample says the API
reads its version numbers from the code, leave the two version lines commented
out: numbers 3 and 4 then always match the API image. Older releases need them
set; take them from `STACK_VERSION` rather than writing the number in:

```php
$CFG->stackmaximaversion = getenv('STACK_VERSION');
$CFG->version = getenv('STACK_VERSION');
```

Raise `$CFG->castimeout` if the CAS is slow, for example when the amd64 images
run under emulation on another architecture.

**Compose file.** Adapt `api/docker/docker-compose.yml`:

```yaml
services:
  maxima:
    image: mathinstitut/goemaxima:${STACK_VERSION}-latest
    tmpfs:
      - "/tmp"
    restart: unless-stopped
    cap_add:
      - SETGID
      - SETUID
    cap_drop:
      - ALL
    environment:
      GOEMAXIMA_QUEUE_LEN: 32
    read_only: true
  stack:
    # Or stackmaths/stackapi:${STACK_VERSION}-latest, where it is published.
    image: stackapi-local:${STACK_VERSION}
    restart: unless-stopped
    environment:
      STACK_VERSION: ${STACK_VERSION}
    volumes:
      - ./config.php:/srv/stack/api/config.php:ro
    ports:
      - '3080:80'
```

Put `STACK_VERSION=<version>` in a file `.env` beside the compose file, where
`docker compose` reads it, and start the API with
`docker compose -f docker-compose.yml up -d`. The service name
`maxima` matters: the API finds Maxima at `http://maxima:8080/maxima` unless
`MAXIMA_URL` says otherwise.

**Check it.** Render an empty question, which the API fills with defaults, and
read the version it reports:

    curl -s http://localhost:3080/render -H 'Content-Type: application/json' \
      -d '{"questionDefinition": "<quiz><question type=\"stack\"></question></quiz>"}'

The response's `aboutapi.stackmaxima` must be the target version. If every
request returns the "STACK-Maxima libraries ... does not match" error, the
four numbers above disagree.

## Use it

Every route is `POST` with a JSON body, and every body carries
`questionDefinition`: the Moodle XML of exactly one question, as a string. The
API rejects a file holding several questions, so split it first, one
`<quiz><question>...</question></quiz>` per request. Omitted elements take the
API's defaults (`api/questiondefaults.yml`), not a site's settings.

| Route | Use it to | Extra fields |
|---|---|---|
| `/test` | run the question's tests on every deployed seed | none |
| `/render` | see one variant: its text, general feedback and question note | `seed` |
| `/grade` | mark one answer and see its route through each PRT | `seed`, `answers` |
| `/validate` | see how STACK validates one input | `inputName`, `answers` |
| `/diff` | list the elements that differ from the API's defaults | none |

A `seed` must be one of the question's deployed seeds ("The specified seed
belongs to no deployed variant" otherwise); without one the API uses the
first. A randomised question with no deployed seeds cannot be
rendered or graded, and `/test` returns no results for it.

### Run the tests

`POST /test`. The response has question-level `messages` and
`isupgradeerror`, and `results`, keyed by seed. For each seed check `passes`,
`fails` and `messages`; each failing entry in `outcomes` has a `reason` naming
what did not match (score, penalty or answer note), and its PRT outcomes give
the actual and expected `answernote`. An empty `results` is not a pass.

### Choose deployed seeds

`/render` returns `questionnote` for a seed, so a question's variants can be
surveyed before seeds are chosen. In a scratch copy of the question, deploy a
range of candidate seeds, say 1 to 50, and render each:

    {"questionDefinition": "...", "seed": 17}

Keep one seed for each distinct note you want, check those variants are not
degenerate, and deploy only those in the real question. Then run `/test`.

### Try an answer

`POST /grade` with `answers` mapping each input name to what a student would
type, for example `{"ans1": "3*x^2"}`. `prtresults` gives each PRT's `score`,
`penalty` and `answernotes`: the full route, including the answer tests' own
notes. Use it to see where an answer goes, then write the route the PRT is
designed to take into a test. Do not copy the route back unchecked.

### When something goes wrong

- **Every question fails with a version-mismatch error:** the four version
  numbers disagree.
- **Blank scores on scattered seeds, while the same test passes elsewhere:** a
  CAS timeout, not a marking fault. Re-run the one question, or raise
  `castimeout`. A real fault fails the same test on every seed.
- **"Question variables invalid" or no response:** check the `maxima`
  container is running and reachable from the `stack` container.
- **Passes in the API, fails on the site:** the site's settings, plugins or
  STACK version differ. The site is the final check.
