class MeldContainer {
    constructor(selector, card_width, card_height, card_separation) {
        this.cards = $(selector);
        this.container = this.cards.parent();
        this.card_count = this.cards.length;
        this.card_width = card_width;
        this.card_height = card_height;
        this.card_separation = card_separation;
    }

    clear() {
        this.container.empty();
    }

    show()
    {
        const width = ((((this.card_count - 1) * this.card_width) * this.card_separation) + this.card_width) * 1.03;
        const height = this.card_height * (1 + (this.card_count / 100));

        this.container.css('width', width  + 'px');
        this.container.css('height', height  + 'px');
        this.container.fadeIn(200);
    }

    getContainer() {
        return this.container;
    }
}