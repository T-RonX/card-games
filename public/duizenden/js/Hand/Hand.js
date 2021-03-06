class Hand {
    constructor(z_fighter, selector, hand_cards, path_draw_from_discarded, path_draw_from_undrawn, card_width, card_height, card_separation) {
        this.z_fighter = z_fighter;
        this.selector = selector;
        this.card_width = card_width;
        this.card_separation = card_separation;
        this.hand_container = new HandContainer(hand_cards, selector, this.createCardElement);
       // this.cards = $(selector);
        this.card_fan = new Fan(this.hand_container, card_separation, card_width, card_height, .013, -Math.ceil(card_height * .03), Math.ceil(card_width / 2), true, 0, z_fighter, false, true);
        this.hover_animator = new HandHoverAnimator(this.hand_container, card_width, card_height, Math.ceil(card_height * .175));
        this.dropable_hand = new DroppableHand(this, path_draw_from_discarded, path_draw_from_undrawn);
    }

    createCardElement(identifier, index){
        return $(`<div data-card-order="${index}" data-card-id="${identifier}" class="card hand ${CardHelper.getValueFromId(identifier)} draggable"></div>`);
    }

    initialize() {
        this.initializeCards();
    }

    redraw(card_width, card_height) {
        this.card_width = card_width;
        this.card_fan.redraw(card_width, card_height, -Math.ceil(card_height * .03), Math.ceil(card_width / 2));
        this.hover_animator.setParameters(card_width, card_height, Math.ceil(card_height * .175));
        this.resizeLocalHandContainer();
    }

    initializeCards() {
        this.card_fan.positionCards();

        if (this.hand_container.getCards().length > 1)
        {
            this.hover_animator.setAnimations();
        }

        this.dropable_hand.makeDropable();
        this.makeCardsSelectable();
        this.makeCardsDraggable();
        this.resizeLocalHandContainer();
        this.hand_container.show();
    }

    makeCardsSelectable() {
        for (const card of this.hand_container.getCards()) {
            const event = (e, x) => {
                let card = $(e.target);

                if (!card.data('dragger').isDragging()) {
                    $(e.target).toggleClass('selected');
                }
            };

            let events = $._data(card.get(0), 'events');
            let mouse_events = events ? events.mouseup : [];

            let has_event = false;

            $.each(mouse_events, function(i,o) {
                has_event = has_event || (o.handler.toString() === event.toString());
            });

            if (!has_event) {
                card.mouseup(event);
            }
        }
    }

    makeCardsDraggable() {
        for (const card of this.hand_container.getCards()) {
            let dragger = new HandCardDragger(this.z_fighter, card);
            card.data('dragger', dragger);
            card.draggable({
                distance: 5,
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

    getCardElementAt(index) {
        return this.hand_container.getCardElementAt(index);
    }

    resizeLocalHandContainer() {
        if (this.hand_container.getCards().length) {
            let width = ((((this.hand_container.getCards().length - 1) * this.card_width) * this.card_separation) + this.card_width) * 1.3;
            const vmin = this.vmin(64.6);

            if (width < vmin) {
                width = vmin;
            }
            $('#hand-local').css('min-width', width + 'px');
        }
    }

    redrawCards() {
        this.hand_container.createCards();
        this.initializeCards();
        this.resizeLocalHandContainer();
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

    vh(v) {
        var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
        return (v * h) / 100;
    }

    vw(v) {
        var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        return (v * w) / 100;
    }

    vmin(v) {
        return Math.min(this.vh(v), this.vw(v));
    }
}