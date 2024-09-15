// Router.ts
export class Router {
    routes: { [path: string]: () => void } = {};

    constructor() {
        window.addEventListener('popstate', this.handleRouteChange);
    }

    addRoute(path: string, handler: () => void) {
        this.routes[path] = handler;
    }

    navigate(path: string) {
        window.history.pushState({}, '', path);
        this.handleRouteChange();
    }

    handleRouteChange = () => {
        const path = window.location.pathname;
        if (this.routes[path]) {
            this.routes[path]();
        }
    };
}