class Stream {
    constructor(options) {
        this.media = null;

        this.options = _.defaultsDeep(options, {
            constraints: {
                video: true,
                audio: false
            }
        });

        console.log(this.options);
    }

    getMedia() {
        return new Promise((resolve, reject) => {
            navigator.getUserMedia(this.options.constraints, media => {
                this.media = media;
                resolve(media);
            }, error => {
                console.error(error);
                reject(error);
            });
        });
    }

    open() {
        return this.getMedia();
    }
}

export default Stream
