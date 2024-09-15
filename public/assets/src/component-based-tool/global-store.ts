// GlobalStore.ts
export class GlobalStore {
    private state: Record<string, any> = {};

    getState(key: string) {
        return this.state[key];
    }

    setState(key: string, value: any) {
        this.state[key] = value;
        document.dispatchEvent(new CustomEvent(`store:${key}`, { detail: value }));
    }

    subscribe(key: string, callback: (value: any) => void) {
        document.addEventListener(`store:${key}`, (event: any) => {
            callback(event.detail);
        });
    }
}

/* // Example usage in a component
import { GlobalStore } from './GlobalStore';

export class AuthComponent extends BaseComponent {
    store = new GlobalStore();

    constructor() {
        super();
        this.store.subscribe('user', this.onUserChange);
    }

    onUserChange = (user: any) => {
        console.log('User updated:', user);
    };
} */
