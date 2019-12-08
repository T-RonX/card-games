class CardDragger {
    constructor(z_fighter, card) {
        this.z_fighter = z_fighter;
        this.is_dragging = false;
        this.start_y = card.offset().top - $(window).scrollTop();
        this.start_x = card.offset().left - $(window).scrollLeft();
    }

    start() {
        return (event, ui) => {
            let card = $(event.target);
            this.is_dragging = true;
            card.css('z-index', this.z_fighter.up());
            drag_source = card.parent().attr('id');

            $('#discarded-pool').closest('.pool').addClass('droppable');
            card.parent().find('.card-hand-dropper').css('display', 'block');
        }
    }

    stop() {
        return (event, ui) => {
            let card = $(event.target);
            this.is_dragging = false;

            card.css('z-index', this.z_fighter.down());

            $('#discarded-pool').closest('.pool').removeClass('droppable');
            card.parent().find('.card-hand-dropper').css('display', 'none');
        }
    }

    isDragging() {
        return this.is_dragging;
    }
}