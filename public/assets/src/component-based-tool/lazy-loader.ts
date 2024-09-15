export class LazyLoader {
    static async loadComponent(path: string): Promise<any> {
        const module = await import(path);
        return module.default;
    }
}
/* 
// Usage example:
LazyLoader.loadComponent('./components/MyComponent')
    .then((Component) => {
        new Component().mount();
    });
 */