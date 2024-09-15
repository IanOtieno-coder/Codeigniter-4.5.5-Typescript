import { Component } from "../../component-based-tool/base-component";

export default class Sidebar extends Component {
    constructor() {
        super();
    }

    render() {
        return this.html`
            <div class="bg-slate-300 w-[200px] fixed left-0 top-0 h-full">
                Sidebar goes here
            </div>
        `
    }
}