"use strict";

export class ASCredit extends HTMLElement {
    static get observedAttributes() {
        return ["closed"];
    }

    constructor() {
        super();

        var shd = this.attachShadow({
            mode: "closed"
        });
        shd.innerHTML = `
            <style>
            .as-credit {
                position: absolute;                
                bottom: 9px;
                left: 15px;
                font-size: 14px;
                color: #444 !important;
                text-align: center;
                text-decoration: none;
            }

            .as-credit a {
                text-decoration: none;
                color: #444 !important;
                font-weight: bold;
            }

            .as-credit a img {
                margin-bottom: -3px;
            }

            :host{
                display: block !important;
                opacity: 1 !important;
                filter: opacity(100%) !important;
                transform: scale(1) !important;
                transform: translate(0, 0) !important;
                clip-path: circle(100%) !important;
                visibility: visible !important;
                position: relative !important;                
                bottom: 30px !important; 
                left: 0px !important; 
                width: 100% !important; 
                height: 35px !important;
                background: #ffffffaa !important;
            }
            </style>
            <div class="as-credit"><a href="https://www.aeroscroll.com" target="_blank">Created with <img height="20" alt="Aeroscroll Gallery" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAAAeCAYAAADO4udXAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyVpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDYuMC1jMDAzIDExNi5kZGM3YmM0LCAyMDIxLzA4LzE3LTEzOjE4OjM3ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgMjEuMiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MTZGMUQzRjBBQzBDMTFFRTlBNTNEODFEQTBENTY0NEQiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MTZGMUQzRjFBQzBDMTFFRTlBNTNEODFEQTBENTY0NEQiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoxNkYxRDNFRUFDMEMxMUVFOUE1M0Q4MURBMEQ1NjQ0RCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoxNkYxRDNFRkFDMEMxMUVFOUE1M0Q4MURBMEQ1NjQ0RCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PppHLLIAAAxgSURBVHja7FsJ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"/></a></div>
        `;
    }

    connectedCallback() {}
}
window.customElements.define("as-credit", ASCredit);