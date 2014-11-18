/*globals JIGIT, webix*/
/*jslint unparam: true */

var JIGIT = JIGIT || {};
(function () {
    'use strict';
    JIGIT.Form = {
        defaultRequestCallback: function (text, xml) {
            var response = xml.json();
            if (response.has_errors) {
                webix.message(response.messages_html, 'alert');
            }
        },
        showInvalidFormMessage: function (message) {
            message = message || 'Please fill up required items.';
            webix.message('<b style="color: #ff0e10">' + message + '</b>', 'info', 8000);
        }
    };
    JIGIT.Panel = {
        form: null,
        setOptions: function (options) {
            this.form.elements.low.define(
                'options',
                options.low
            );
            this.form.elements.top.define(
                'options',
                options.top
            );
            this.form.elements.ver.define(
                'options',
                options.ver
            );
        }
    };
}());
