module.exports = {
    base: '/',
    title: 'GetLoy Integration Library for PHP',
    themeConfig: {
        markdown: {
            toc: { includeLevel: [1, 2, 3] },
        },
        sidebarDepth: 3,
        lastUpdated: true,
        repo: 'getloy/getloy-php',
        docsDir: 'docs',
        editLinks: true,
        nav: [],
        sidebar: {},
        evergreen: true // disable ES5 transpilation and polyfills for IE
    }
}
