# Iframe blocks

The iframe block is the basis for most [dynamic blocks](Dynamic_blocks.md), with it one can construct a sandboxed iframe container within the question text and define freely what it contains. One can load almost anything into that iframe assuming no firewalls are blocking the traffic and the content is being served with sutiable CORS headers. The sandbox logic makes sure that even if the content loaded would turn out to be evil it cannot directly access the surrounding parts of the VLE.

## Iframe block ##

The `[[iframe]]` block will simply create a visible iframe with the default size of 500px x 400px, the size and aspect-ratio settings can be tuned with the same attributes as `[[jsxgraph]]` blocks. The frame can also be made hidden by setting the `hidden`-attribute. Scrolling can be disabled by setting `scrolling='false'`. Finally, one can set a `title` attribute should it be necessary, e.g., to identify the correct sandbox during debugging, there is a default title with running numbering.

The contents of the block will be directly outputted into the body of the generated document, with the exception of `[[style]]` and `[[script]]` blocks. Those will be mapped to the head of the generated document.

The generated document will contain the following things in addition to whatever you add to it:
  * The `lang` attribute of the `<html>` element will match the VLE reported language.
  * There is a `<script>` element defining a `const FRAME_ID` which STACK-JS would use for targetted communication.

Minimal usage of this block is as follows:

```
[[iframe]]
<h1 style="color:red;">Red text in a box</h1>
[[/iframe]]
```
Note that that box will not follow any style rules the surrounding VLE might have. Also note that the contents of the box might not be as accessible as things outside the box.

**You may not place an `[[iframe]]` inside an `[[iframe]]`.** Well you may but it won't work. This applies to all members of the family, so no `[[jsxgraph]]` or `[[adapt]]` inside eachother or other `[[iframes]]`.

## Style block ##

The `[[style]]` block only functions inside blocks of the `[[iframe]]` family (e.g., you could use it inside `[[jsxgraph]]`). Regardles on where inside the block it is it will generate its output as an element in the head of the generated document. You can either use it as equivalent of `<style>` by writing the contents of such an element inside the block. Or as `<link rel="stylesheet" href="..."/>` if you define the `href` attribute. You may also affect the values of `media`, `blocking`, `title`, `nonce`, `type`, and `crossorigin` attributes by setting them.

```
[[iframe]]
[[style]] h1 {color:red;} [[/style]]
<h1>Red text in a box</h1>
[[style href="address of some remote stylesheet"/]]
[[/iframe]]
```

## Script block ##

The `[[script]]` block only functions inside blocks of the `[[iframe]]` family (e.g., you could use it inside `[[jsxgraph]]`). Regardles on where inside the block it is it will generate its output as a script-element in the head of the generated document. You may set `type`, `blocking`, `src`, `integrity`, `nonce`, and `async` attributes to tune the same ones in the generated element. And naturally you may include contents as you wish.

Do note that should you only want to do some JavaScripting with STACK-JS, you do not need to setup an `[[iframe]]` with `[[script]]` inside it yourself. You can use the `[[javascript]]` block which will automatically build a hidden iframe and load `stack_js`, it will also allow one to setup `input-refs` in the style of `[[jsxgraph]]`. In that block, the content will go directly into a `<script type="module">` element.





# Somewhat complicated example #

A problem with `[[iframes]]` is that they are not really part of the normal document flow and can thus pose problems for accessibility. However, due to security reasons they are necessary. Do note that `[[iframes]]` do not need to be visible and it is possible to transfer static content to and from them. In this example, we apply syntax highlighting to a block of code that is already visible on the VLE side and then return the modified but still static content on top of the original. The hidden iframe should not cause accessibility issues and if the scripting happens to not run the original content will still stay visible.

```
<div id="[[quid id='code'/]]">
<pre><code class="language-c">[[entityescape]]
# include <stdint.h> // uint32_t

float Q_rsqrt(float number)
{
  union {
    float    f;
    uint32_t i;
  } conv = { .f = number };
  conv.i  = 0x5f3759df - (conv.i >> 1);
  conv.f *= 1.5F - (number * 0.5F * conv.f * conv.f);
  return conv.f;
}
[[/entityescape]]</code></pre>
</div>
[[javascript]]
[[comment]]Use style and script blocks to load in highlight.js, crossorigin="anonymous" for style transfer.[[/comment]]
[[style href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/srcery.min.css" crossorigin="anonymous"/]]
[[script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"/]]

[[comment]]Then load the stackcssutils library, to do some tricks.[[/comment]]
import {stack_css_utils} from '[[cors src="stackcssutils.min.js"/]]';

[[comment]]Load the content from the VLE side, store it in the iframe and then highlight it then map it back.[[/comment]]
stack_js.get_content("[[quid id='code'/]]").then((content) => {
	/* Create an element to put this in. */
	const holder = document.createElement("div");
	holder.innerHTML = content;
	document.body.appendChild(holder);

	/* Apply highlighting. */
	hljs.highlightAll();

	/* Inline the new styles. */
	stack_css_utils.inline();

	/* Transfer the styled thign back. */
	stack_js.switch_content("[[quid id='code'/]]", holder.innerHTML);
});
[[/javascript]]
```
In that example, we use `[[quid/]]` to produce a question level unique identifier, `[[entityescape]]` to avoid having to escape certain chars on our own, `[[javascript]]` to setup an `[[iframe]]` with certain handy features, e.g, `stack_js` already present. Then we use `[[style]]` and `[[script]]` to load some external libaries into that `[[iframe]]`, and `[[cors]]` to also load some local libraries. Finally, using `stack_js` we read the code from the VLE side and then push it back. Between reading and writing we usel highlight.js to syntax highlight the code and `stack_css_utils` to inline the styles applied so that we can transfer the content without the stylesheets.

This example is possibly overly convoluted, one does not need to transfer code and style around, it can simply exist inside an iframe. But should one want to maximise accessibility and fail safe if the script logic fails the example shows one way of doing so.