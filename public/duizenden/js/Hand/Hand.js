class Hand {
    constructor(z_fighter, selector, hand_cards, path_draw_from_discarded, path_draw_from_undrawn, card_width, card_height, card_separation) {
        this.z_fighter = z_fighter;
        this.selector = selector;
        this.hand_container = new HandContainer(hand_cards, selector, this.createCardElement);
       // this.cards = $(selector);
        this.card_fan = new Fan(this.hand_container, card_separation, card_width, card_height, true, Math.ceil(card_height / 3.5), Math.ceil(card_width / 2), true, 0, z_fighter, false);
        this.hover_animator = new HandHoverAnimator(this.hand_container, card_width, card_height, Math.ceil(card_height * .175));
        this.dropable_hand = new DroppableHand(this, path_draw_from_discarded, path_draw_from_undrawn);
    }

    createCardElement(identifier, index){
        return $(`<div data-card-order="${index}" data-card-id="${identifier}" class="card hand ${CardHelper.getValueFromId(identifier)} draggable"></div>`);
    }

    initialize() {
        this.initializeCards();
    }

    initializeCards() {
        this.card_fan.positionCards(true);

        if (this.hand_container.getCards().length > 1)
        {
            this.hover_animator.setAnimations();
        }

        this.dropable_hand.makeDropable();
        this.makeCardsSelectable();
        this.makeCardsDraggable();
        this.hand_container.show();
    }

    makeCardsSelectable() {
        for (const card of this.hand_container.getCards()) {
            card.mouseup((e, x) => {
                let card = $(e.target);

                if (!card.data('dragger').isDragging()) {
                    $(e.target).toggleClass('selected');
                }
            });
        }
    }

    makeCardsDraggable() {
        for (const card of this.hand_container.getCards()) {
            let dragger = new HandCardDragger(this.z_fighter, card);
            card.data('dragger', dragger);
            card.draggable({
                revert: (is_valid_drop) => {
                    if (!is_valid_drop) {
                        this.redrawCards();
                    }
                },
                revertDuration: 300,
                start: dragger.start(),
                drag: dragger.drag(),
                stop: dragger.stop()
            })
        }
    }

    getHandContainer() {
        return this.hand_container;
    }

    addCards(cards, target) {
        this.hand_container.addCards(cards, target);
        this.initializeCards();
    }

    redrawCards() {
        this.hand_container.createCards();
        this.initializeCards();
    }

    reorderCards(from, to) {
        this.hand_container.reorder(from - 1, to - 1);
        this.redrawCards();
    }

    removeCard(id) {
        this.hand_container.removeCard(id);
        this.redrawCards();
    }

    removeCards(ids) {
        for (const id of ids) {
            this.hand_container.removeCard(id);
        }
        this.redrawCards();
    }
}