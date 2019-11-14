class DroppableMeld {
    constructor(meld_pool, path_extend_meld) {
        this.meld_pool = meld_pool;
        this.path_extend_meld = path_extend_meld;
    }

    makeDropable() {
        this.meld_pool.droppable({
            hoverClass: 'highlight_meld',
            drop: (event, ui) => {
                if (drag_source === 'hand-local-container') {
                    let target = $(event.target);
                    let url = this.path_extend_meld.replace('111', ui.draggable.data('card-id')).replace('000', target.data('meld-id') - 1);
                    ui.draggable.remove();
                    $.post(url, null, function (data) {
                        //location.reload();
                    });
                }
            }
        });
    }
}