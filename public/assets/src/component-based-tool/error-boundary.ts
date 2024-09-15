import { Component } from "./base-component";


export class ErrorBoundary extends Component {
    state = { hasError: false };

    static getDerivedStateFromError(error: Error) {
        return { hasError: true };
    }

    componentDidCatch(error: Error, errorInfo: any) {
        console.error('Error caught:', error, errorInfo);
    }

    render() {
        if (this.state.hasError) {
            return this.html`<h1>Something went wrong.</h1>`;
        }

        return this.props.children;
    }
}
