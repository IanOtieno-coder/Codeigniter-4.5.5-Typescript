import { html } from "lit-html";
import { Component } from "../../../component-based-tool/base-component";
import { ComponentProps } from "../../../component-based-tool/types";

export default class Child extends Component{
    constructor(props?: ComponentProps) {
        super(props)
    }

    render() {
        return this.html`
            <p>Child component</p>
            <button @click=${() => console.log('clicked')}>Click me</button>
        `
    }
}