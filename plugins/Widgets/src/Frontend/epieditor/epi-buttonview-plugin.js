import ButtonView from '@ckeditor/ckeditor5-ui/src/button/buttonview';
import DropdownButtonView from '@ckeditor/ckeditor5-ui/src/dropdown/button/dropdownbuttonview';
import View from "@ckeditor/ckeditor5-ui/src/view";

export class EpiButtonView extends ButtonView {

    constructor(locale) {
        super(locale);
        this.set('symbol', undefined);
        this.set('symbolFont', undefined);
        this.set('symbolStyle', undefined);
        this.symbolView = this._createSymbolView();
    }

    /**
     * Creates a symbol view instance and binds it with button attributes.
     *
     */
    _createSymbolView() {
        const labelView = new View();
        const bind = this.bindTemplate;
        labelView.setTemplate({
            tag: 'span',
            attributes: {
                class: [
                    'ck',
                    'ck-button__symbol',
                    'ck-reset_all-excluded',
                    bind.to('symbolFont', value => 'font_' + value)
                ],
                style: [
                    bind.to('symbolStyle', value => value  || '')
                ]
            },
            children: [
                {
                    text: bind.to('symbol')
                }
            ]
        });
        return labelView;
    }

    render() {
        // TODO: reimplement ButtonView.render() to change the order to
        //       icon, symbol, caption, keystroke
        if (this.symbol) {
            this.children.add(this.symbolView);
        }
        super.render();
    }
}

export class EpiDropdownButtonView extends DropdownButtonView {
    constructor(locale) {
        super(locale);
        this.set('symbol', undefined);
        this.set('symbolFont', undefined);
        this.set('symbolStyle', undefined);
        this.symbolView = this._createSymbolView();
    }

    /**
     * Creates a symbol view instance and binds it with button attributes.
     *
     */
    _createSymbolView() {
        const labelView = new View();
        const bind = this.bindTemplate;
        labelView.setTemplate({
            tag: 'span',
            attributes: {
                class: [
                    'ck',
                    'ck-button__symbol',
                    'ck-reset_all-excluded',
                    bind.to('symbolFont', value => 'font_' + value)
                ],
                style : [
                    bind.to('symbolStyle', value => value  || '')
                ]
            },
            children: [
                {
                    text: bind.to('symbol')
                }
            ]
        });
        return labelView;
    }

    render() {
        if (this.symbol) {
            // TODO: reimplement ButtonView.render() to change the order to
            //       icon, symbol, caption, keystroke
            this.children.add(this.symbolView);
        }
        super.render();
    }
}
