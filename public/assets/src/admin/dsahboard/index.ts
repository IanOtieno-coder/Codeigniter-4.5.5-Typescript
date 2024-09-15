// JavaScript for admin/dsahboard"
import $ from "jquery"
import * as t from './types'
import CounterComponent from "./components/counter"
import Sidebar from "../../globals/components/side-bar"

$(() => {
    const counter = new CounterComponent()
    counter.mount("#app")

    /* Mont sidebar */
    const sidebar = new Sidebar()
    sidebar.mount("#app")
})