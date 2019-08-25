class Hand {
    constructor(z_fighter, card_selector, path_draw_from_discarded, path_draw_from_undrawn, card_width, card_height, card_separation) {
        this.z_fighter = z_fighter;
        this.hand_container = new HandContainer(card_selector);
        this.cards = $(card_selector);
        this.hand_position = new HandPosition(this.cards, card_separation, card_width, card_height);
        this.hover_animator = new HandHoverAnimator(this.cards, card_width, card_height, Math.ceil(card_height * .175));
        this.dropable_hand = new DroppableHand(this.cards, path_draw_from_discarded, path_draw_from_undrawn);
    }

    initialize() {
        this.hand_position.positionCards();

        if (this.cards.length > 1)
        {
            this.hover_animator.setAnimations();
        }

        this.dropable_hand.makeDropable();
        this.makeCardsSelectable();
        this.makeCardsDraggable();
        this.hand_container.show();
    }

    makeCardsSelectable() {
        this.cards.mouseup((e, x) => {
            let card = $(e.target);

            if (!card.data('dragger').isDragging()) {
                $(e.target).toggleClass('selected');
            }
        });
    }

    makeCardsDraggable() {
        this.cards.each((i, e) => {
            let card = $(e);
            let dragger = new HandCardDragger(this.z_fighter, card);
            card.data('dragger', dragger);
            card.draggable({
                revert: 'invalid',
                revertDuration: 300,
                start: dragger.start(),
                drag: dragger.drag(),
                stop: dragger.stop()
            })
        });
    }
}