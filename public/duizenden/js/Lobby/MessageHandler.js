class MessageHandler
{
    constructor(chatarea, player_id) {
        this.player_id = player_id;
        this.chatarea = chatarea;
        this.chatarea.scrollTop(this.chatarea[0].scrollHeight);
    }

    handle(e) {
        const data = JSON.parse(e.data);
        console.log(data);
        this.chatarea.append(this.line(data));
        this.chatarea.scrollTop(this.chatarea[0].scrollHeight);
    }

    nl2br (str) {
        if (typeof str === 'undefined' || str === null) {
            return '';
        }

        str = str.replace(/(\r\n)|(\n\r)|\r/g, "\n");
        str = str.replace(/\n\n\n/g, "\n\n");
        str = str.replace(/\n/g, "<br/>");

        return str;
    }

    line(message) {
        return `<div class="line ${message.id === this.player_id ? 'out' : 'in'}">
                <div class="bubble"></div>
                <div class="message">
                    <div class="name">${jQuery('<div/>').text(message.name).html()}</div>
                    <div>${this.nl2br(jQuery('<div/>').text(message.message).html())}</div>
                    <div class="time">${jQuery('<div/>').text(message.date).html()}</div>
                </div>
            </div>`;
    }
}
