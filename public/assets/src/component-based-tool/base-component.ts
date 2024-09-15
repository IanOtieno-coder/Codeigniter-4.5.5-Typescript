// components/Component.ts

import { ComponentProps, ComponentState, ComponentRef } from './types';
import { html, render as litRender } from "lit-html";

export abstract class Component {
  props: ComponentProps;
  state: ComponentState;
  rootElement: HTMLElement | null;
  refs: ComponentRef;
  memo: any;
  html: typeof html;
  private element: HTMLElement | null

  constructor(props: ComponentProps = {}) {
    this.props = props;
    this.state = {};
    this.refs = {};
    this.memo = {};
    this.rootElement = null;
    this.html = html;
    this.element = null;
    this.componentWillMount();
  }

  // Abstract method that returns lit-html template
  abstract render(): ReturnType<typeof html>;

  // Set state and trigger re-render
  setState(newState: ComponentState) {
    this.state = { ...this.state, ...newState };
    this.update();
  }

  // Mount the component to a DOM element using a selector
  mount(selector: string) {
    const rootElement = document.querySelector(selector);
    if (rootElement) {
      this.element = rootElement as HTMLElement;
      this.update();
      this.componentDidMount();
    } else {
      console.error(`Could not find element with selector: ${selector}`);
    }
  }

  // Re-render the component in the DOM using lit-html
  private update() {
    if (this.element) {
      litRender(this.render(), this.element);
    }
  }

  // Refs system to access DOM elements easily
  createRef(refName: string) {
    this.refs[refName] = { current: null };
  }

  // Assign a DOM element to the ref
  attachRef(refName: string, selector: string) {
    const ref = this.refs[refName];
    if (ref) {
      const element = document.querySelector(selector);
      ref.current = element as HTMLElement | null;
    }
  }

  // Utility function to retrieve the element of a ref
  getRef(refName: string): HTMLElement | null {
    return this.refs[refName]?.current || null;
  }

  // State getter
  getState(): ComponentState {
    return this.state;
  }

  // Props getter
  getProps(): ComponentProps {
    return this.props;
  }

  // Memoization
  memoize(key: string, fn: Function) {
    if (!this.memo[key]) {
      this.memo[key] = fn();
    }
    return this.memo[key];
  }


  protected componentWillMount() {
    // Logic before component mounts
  }

  protected componentDidMount() {
    // Logic after component has mounted
  }

  protected componentWillUnmount() {
    // Logic before component unmounts
  }
}

