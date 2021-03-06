class HandContainer {
    constructor(cards, selector, create_element) {
        this.cards = cards;
        this.container = $(selector);
        this.create_element = create_element;
        this.card_elems = [];
        this.createCards();
    }

    createCards() {
        this.card_elems = [];
        this.container.empty();

        let index = 0;
        for (const card of this.cards) {
            const card_elem = this.createCard(card, ++index);
            this.card_elems.push(card_elem);
            this.container.append(card_elem);
        }
    }

    getCardElementAt(index) {
        return this.container.find(`.card[data-card-order='${index}']`);
    }

    setCards(cards) {
        this.cards = cards;
    }

    createCard(identifier, index) {
        return this.create_element(identifier, index);
    }

    clear() {
        this.container.empty();
    }

    show() {
        this.container.fadeIn(200);
    }

    getCards(plain = false) {
        return plain ? this.cards : this.card_elems;
    }

    getContainer() {
        return this.container;
    }

    addCards(cards, target) {
        cards = Array.isArray(cards) ? cards : [cards];
        this.cards = this.cards.concat(cards);

        if (cards.length === 1)
        {
            const card_elem = this.createCard(cards[0], this.cards.length);
            this.card_elems.splice(target, 0, card_elem);
            if (target < 1) {
                this.container.prepend(card_elem);
            } else {
                card_elem.insertAfter(this.container.find(`.card:nth-child(${target})`));
            }
        } else {
            for (const card of cards) {
                const card_elem = this.createCard(card, this.cards.length);
                this.card_elems.push(card_elem);
                this.container.append(card_elem);
            }
        }

        this.resetOrderIds();
    }

    resetOrderIds() {
        let order = 1;

        for (const card of this.card_elems) {
            card.attr('data-card-order', order);
            ++order;
        }
    }

    reorder(source, target) {
        if (source === target) {
            return;
        }

        if (!source in this.cards || (target < -1 && !target in this.cards)) {
            return;
        }

        const moving_card = this.cards.splice(source, 1)[0];

        if (target === -1) {
            this.cards.unshift(moving_card);
        } else {
            this.cards.splice(source > target ? target + 1 : target, 0, moving_card);
        }
    }

    removeCard(id) {
        const i = this.cards.indexOf(id);

        if (i > -1) {
          this.cards.splice(i, 1);
        }
    }
}