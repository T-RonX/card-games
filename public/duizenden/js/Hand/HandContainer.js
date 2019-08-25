class HandContainer {
    constructor(selector) {
        this.cards = $(selector);
        this.container = this.cards.parent();
    }

    setCards(cards) {
        this.clear();
        this.cards = cards;
        this.container.html(this.cards);
    }

    clear() {
        this.container.empty();
    }

    show()
    {
        this.container.fadeIn(200);
    }
}