# Netlogix.EsiRendering
This package provides a simple way to render [edge side includes](https://de.wikipedia.org/wiki/Edge_Side_Includes) in Neos. This alows you to
individually cache parts of the page (e.g. header/footer).

We recommend using this package together with [flowpack/varnish](https://github.com/Flowpack/varnish) for Varnish integration.

## Install package
`composer require netlogix/esi-rendering`

## Usage
To render a fusion path as ESI, you can use the `Netlogix.EsiRendering:RenderAsEsi` Fusion implementation:

```html
renderer = afx`
    <p>This is rendered outside of the esi</p>
    
    <Netlogix.EsiRendering:RenderAsEsi node={props.site} cacheIdentifier="my-esi">
        <p>This is rendered inside of the esi</p>
        
        <p>The given node is available as {node}</p>
    </Netlogix.EsiRendering:RenderAsEsi>
`
```

This will render a `<esi:include src="...">` tag after the first `<p>`.

ESIs are not used in the Neos backend, instead the content will be rendered directly.

## Debugging
To get the ESI uri, you can set the following setting to `true`:

```yaml
Netlogix:
  EsiRendering:
    debug: true
```

This will render a `link` before the ESI:
```html
<link rel="esi:include" esi-identifier="my-esi" context-node="/sites/my-site@live" href="/esirendering?fusionPath=...">
```

This is enabled by default for the Development context.
