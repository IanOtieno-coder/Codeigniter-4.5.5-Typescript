// types/index.d.ts

export interface ComponentProps {
    [key: string]: any;
  }
  
  export interface ComponentState {
    [key: string]: any;
  }
  
  export interface ComponentRef {
    [key:string] : {current: HTMLElement | null;}
  }
  