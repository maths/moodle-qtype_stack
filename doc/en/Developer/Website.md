# Updating the Online Docs

The STACK documentation is available online on [docs.stack-assessment.org](https://docs.stack-assessment.org/en/). The docs are automatically built whenever this repository is pushed to. This documentation details how to update the styling of the online docs.

#### What is not covered in this document

Instructions for updating the main website [www.stack-assessment.org](https://www.stack-assessment.org). The main website is built in the [stack-web](https://github.com/maths/stack-web) repository, and has [its own documentation](https://github.com/maths/stack-web/blob/master/README.md).

## Framework

The website is built using [MkDocs](https://www.mkdocs.org/), a static site generator which converts STACK documentation, within the `/doc` directory, into HTML files and pushes them to the `gh-pages` branch. The website structure mirrors the file structure: the file `doc/en/Authoring/Answer_Tests/index.md` will be available on `docs.stack-assessment.org/en/Authoring/Answer_tests/`. Every sub-folder has an `index.md` file that will take that folder's name on the website: `doc/en/Authoring/index.md` will be available on `docs.stack-assessment.org/en/Authoring/`.

MkDocs is configured in the `mkdocs.yml` file. MkDocs has a full list of [available configuration options](https://www.mkdocs.org/user-guide/configuration/). MkDocs can either generate the navigation bar automatically, or accept a custom navigation configuration in the `nav` variable. The online docs uses the first option. The advantage is that new files are automatically added to the navigation bar when they are added to the repository. The disadvantage is that we cannot tweak the navigation bar manually. The online docs get around this with a number of workarounds.

MkDocs cannot display MathJax out-of-the-box, so we use the markdown extension [mdx_math](https://github.com/mitya57/python-markdown-math), specified in `mkdocs.yml`, with the variable `extra_javascript` set to include MathJax.

MkDocs can accept a third-party theme, and the main STACK website uses the [Material Theme](https://squidfunk.github.io/mkdocs-material/), mainly for its better search display and adaptive structure.

The site is hosted by [GitHub Pages](https://pages.github.com/) from the `gh-pages` branch. A workflow under `.github` ensures that MkDocs runs its command `mkdocs gh-deploy` every time the repository is pushed to, which rebuilds the website and pushes the built HTML piles to the `gh-pages` branch. This overrides all the files currently in the `gh-pages` branch, so **you must never edit files directly in the `gh-pages` branch**.

## STACK-specific Workarounds

The online docs use a number of workarounds to make MkDocs compatible with the STACK docs.

#### Dealing with the structure of the docs

MkDocs expects a single folder to contain all the website files, starting with an `index.md` file that is to be the landing page. However, STACK's docs have a different structure: `doc` contains the `content` folder, and the two language folders where the documentation is provided. The landing page is under `en/index.md`.

To work around this, we set `doc` to be the location of the docs, and use the plugin [mkdocs-exclude](https://pypi.org/project/mkdocs-exclude/) to unwanted files from the navigation, such as `/de` and `en/Site_map.md`. We then use the plugin [mkdocs-redirects](https://pypi.org/project/mkdocs-redirects/) to redirect the non-existing `index.md` file to the true landing page `en/index.md`. The effect of this is that [docs.stack-assessment.org](https://docs.stack-assessment.org) will always redirect to [docs.stack-assessment.org/en/](https://docs.stack-assessment.org/en/).

We also update the sidebar navigation, such that it does not show the `en` directory. This is done by overriding Material's `nav-item` partial in the `site_overrides/partials/` directory. 

#### Dealing with the theme

The online docs make a number of custom changes to the Material theme.

* We use the basic MkDocs search algorithm, by including a script in the `{% block config %}` block under `site_overrides/main.html`
* In `site_overrides/partials/logo.html` we customise how the STACK logo is shown. This is necessary, since the STACK logo is not square.
* We add a custom footer in the `{% block footer %}` block under `site_overrides/main.html`. In here we copy Material's primary and secondary sidebar classes, such that the footer collapses when the sidebar navigation and search bar does. It was necessary to make an identical copy of the `md-sidebar--primary` class called `md-sidebar--primary_footer`, to avoid the sidebar breaking when zoomed.

## Updating the documentation

When you change the documentation, the website automatically updates as well. This introduces some new limitations to the sort of elements that can be included in the documentation. This is documented in the [Documentation](Documentation.md) file.

### Updating the style

The online docs uses a custom [CSS stylesheet](https://github.com/maths/moodle-qtype_stack/blob/master/doc/custom.css) which you can edit. This stylesheet builds upon the stylesheets of MkDocs and Material.

You can also edit the theme directly. Any file in the `site_overrides` folder will override files of the same name in [Material's directory](https://github.com/squidfunk/mkdocs-material/tree/master/material). The `main.html` file is designed to make it easy to override some [predetermined blocks](https://squidfunk.github.io/mkdocs-material/customization/#overriding-blocks), but sometimes it is necessary to override files directly. Notice we directly override some of [Material's partials](https://github.com/squidfunk/mkdocs-material/tree/master/material/partials).

## Testing the website locally

Before adding major style changes to the online docs, you are encouraged to test your changes locally. For this, you will need to install MkDocs and all the required extensions.

1. [Install MkDocs](https://www.mkdocs.org/), including its requirements.
2. Install Material with `pip install mkdocs-material`
3. Install the markdown extension with `pip install https://github.com/mitya57/python-markdown-math/archive/master.zip`
4. Install the exclude plugin with `pip install mkdocs-exclude`
5. Install the redirect plugin with `pip install mkdocs-redirects`

You can run a local version of the website with the command `mkdocs serve`. This will make your local version available on the IP `http://127.0.0.1:8000/`.

Please test that your changes work on:

- The following browsers: Chrome, Firefox, Safari, Edge.
- The following sizes: Computer, tablet, mobile. Chrome's "inspect" tool works well for this.

