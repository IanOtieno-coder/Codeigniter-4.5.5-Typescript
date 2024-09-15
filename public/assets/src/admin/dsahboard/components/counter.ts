import { Component } from "../../../component-based-tool/base-component";
import { ComponentProps } from "../../../component-based-tool/types";
import Child from "./child";

export default class CounterComponent extends Component {
    constructor(props?: ComponentProps) {
        super(props)
        this.state = { count: 0 }
    }

    render() {
        return this.html`
        <div class="w-full items-center flex justify-center h-screen">
            <div class="bg-slate-200 rounded-sm p-2 text-center w-[300px]">
                <p> Counter: ${this.state.count} </p>
                <button class="bg-gray-100 p-1 rounded-sm shadow-sm w-8 h-8" @click=${() => this.setState({ count: this.state.count - 1 })}>-</button>
                <button class="bg-gray-100 p-1 rounded-sm shadow-sm w-8 h-8" @click=${() => this.setState({ count: this.state.count + 1 })}>+</button>

                <!-- Render the child and you can pass the props from the parent to it as you do so -->
                ${new Child().render()}
            </div>
        </div>
        `
    }
}