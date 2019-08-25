class HandCardDragger extends CardDragger {
    drag() {
        return (event, ui) => {
            let card = $(event.target);
            let center_x = 0;
            let center_y = 1400;
            let x = card.position().left;
            let y = card.position().top;
            let rotate = MathHelper.radiansToDegrees(Math.atan2(y - center_y, x - center_x)) + 90;
            let y_p = (((card.offset().top - $(window).scrollTop()) / window.innerHeight) * 100);
            let x_p = (((card.offset().left - $(window).scrollLeft()) / window.innerWidth) * 100);
            let moved_y = (Math.abs(this.start_y - card.offset().top)) / window.innerHeight * 100;
            let moved_x = (Math.abs(this.start_x - card.offset().left)) / window.innerWidth * 100;
            let allowed_angle = 1 - Math.max(0, (Math.min(25, Math.max(moved_y, moved_x)) * 4)) / 100;

            rotate = Math.floor((y_p / 100) * rotate) * allowed_angle;

            card.data('rotation_angle', rotate);
            card.css('transform', 'rotate(' + rotate + 'deg)');
            card.css('opacity', .6/*Math.max(.3, 1 - allowed_angle)*/);
        }
    }
}