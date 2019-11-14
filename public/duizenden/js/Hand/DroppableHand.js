class DroppableHand {
    constructor(hand, path_draw_from_discarded, path_draw_from_undrawn) {
        this.path_draw_from_discarded = path_draw_from_discarded;
        this.path_draw_from_undrawn = path_draw_from_undrawn;
        this.hand = hand;
        this.is_hand_dropping = false;
    }

    makeDropable() {
        this.hand.getHandContainer().getContainer().find('.card-hand-dropper').droppable({
            hoverClass: 'highlight',
            tolerance: "pointer",
            drop: async (e, ui) => {
                //console.log(ui.draggable.data('card-order') + ' to ' + $(this).data('card-order'));

                if (drag_source === 'discarded-pool') {
                    let cards = [];

                    this.hand.getHandContainer().getContainer().find('.selected').each(function (item) {
                        cards.push($(this).data('card-id'));
                    });

                    const target = $(e.target);
                    const target_id = target.data('card-order');

                    if (cards.length) {
                        $.post(this.path_draw_from_discarded.replace('111', '0').replace('000', cards.join()), null, function (data) {
                            //location.reload();
                        });
                    } else {
                        $.post(this.path_draw_from_discarded.replace('111', target_id), null, function (data) {
                            //location.reload();
                        });
                    }
                }

                if (drag_source === 'hand-local-container' && !this.is_hand_dropping) {
                    this.is_hand_dropping = true;

                    const target = $(e.target);
                    const target_id = target.data('card-order');
                    const source_id = ui.draggable.data('card-order');

                    if (target_id === undefined || source_id === undefined) {
                        return;
                    }

                    //await new Promise(resolve => setTimeout(resolve, 0));
                    this.hand.reorderCards(source_id, target_id);
                    $.post('/duizenden/reorder-card/000/000'.replace('000', source_id).replace('000', target_id), null, (data) => {
                        this.is_hand_dropping = false;
                    });
                }

                if (drag_source === 'undrawn-pool') {
                    const target = $(e.target);
                    const target_id = target.data('card-order');

                    $.post(this.path_draw_from_undrawn.replace('000', target_id), null, function (data) {
                        //location.reload();
                    });
                }
            }
        });
    }
}