class MessageHandler
{
    constructor(textarea) {
        this.textarea = textarea;
        this.textarea.scrollTop(this.textarea[0].scrollHeight);
    }

    handle(e) {
        const data = JSON.parse(e.data);
        this.textarea.val(`${this.textarea.val()}\n${data.name}: ${data.message}`);
        this.textarea.scrollTop(this.textarea[0].scrollHeight);
    }
}
