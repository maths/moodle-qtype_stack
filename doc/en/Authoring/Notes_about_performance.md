# Notes about performance

Authoring style may affect the performance of the questions—the technical performance in the form of response times and bandwidth usage. Of course, one could say many things about the pedagogical performance of questions, but that is something that this document skips.

Some things have changed in 4.4, but most of this applies to all STACK materials.

The further down you read this, the less likely it is to apply to normal usage.

When we are talking about affecting the performance, in most cases, the effects are linear; adding more inputs means that we need to process more inputs, and the time used grows accordingly. The bits that matter are the non-linear ones, e.g. unknown execution lengths or filling the buffers with unused data might simply push things over some limit and increase the processing time unexpectedly.

## Deployed variants are good!

While not everyone uses them and not everyone needs to use them, they do improve performance and allow caching to work. They also allow one to visually inspect the randomised parameters and thus help detect wildly differing difficulty levels.

If you are running on a truly large scale or if your servers cannot keep up, always look at [deployed variants](./Deploying.md) first.

Just make your question notes informative, the prefered way is to show the question and the answer for this particular variant in short form; a simple parameter list may help, but it is often less than helpful.

## Randomisation loops are really bad!

If you need to randomise something and then select only the ones that match certain conditions, you will need to know that the loop will be repeated every time the question receives input, often more than once.

So don't write anything like this:

	foo: rand(.....);
	while not is_good(foo) do foo: rand(.....);

Not only is there a risk that some seed of the random number generator never leads to parameters that end up being "good", but that can also lead to wildly varying execution times and even timeouts. 

If you cannot write code that directly randomises a "good" result, you should use deployed variants and question-tests to pre-randomise and select only the cases where the randomisation is "good", the key is to use question-tests as the filter here.

## Big strings or things you might not want to place in question-variables.

In the STACK question model, it is assumed that whatever is in the question-variables or in any other code/logic block is data and needs to be fully transferred into the CAS during all processing steps of the question; conversely, whatever is present in the question-text or other text content is assumed to be safe and not "data". We do special sidelining of such safe "not data" and don't send it all the way to CAS if we can avoid it, and it still rejoins the output once necessary.

This means that if you place big strings on the logic side without actually using them there, you are probably wasting resources. If that text is intended as something to be outputted in the text itself then one should look at putting it into the text itself, maybe using [CASText features](./Question_blocks/index.md) that allow conditional inclusion if need be.

Typically one can end up in this type of situation if one converts materials from other systems and chooses to build their output on the logic side. Sometimes people do image inclusions, either SVG or base64 style content and end up slowing their systems if they add too many of them.

The issue is not only about the transfer of large strings or other content taking bandwith, cache space, and parsing resources but also about transferring things not needed. Those question variables are always included in the evaluation session even when that session would not actually render question-text e.g. when doing input-validation.

## Number of inputs directly affects performance.

Currently (pre input2), the number of inputs in a question affects the number of CAS sessions needed for the evaluation and validation of answers. In general, you will have to live with it, but if you happen to do extensive scripting on the client-side, you might want to know that it is only the number of inputs in the question model that matters, not the number of things you place into those inputs. So if you are pushing the limits, maybe map multiple client-side inputs into a few hidden actual inputs.

## Number of PRTs affected the performance.

If you have large numbers of PRTs and are not running 4.4 or later, you might gain significant performance boosts if you update to 4.4. In the earlier versions, every PRT was evaluated separately in their own CAS session, but now they get handled in a single session. Every new session always incurs a performance overhead, so joining them does give us some benefits.