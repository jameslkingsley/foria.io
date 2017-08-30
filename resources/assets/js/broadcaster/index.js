import Stream from './stream';

class Broadcaster {
    constructor(options = {}) {
        this.options = options;

        this.stream = new Stream;
    }

    start() {
        this.stream.open().then(media => {
            this.options.input.srcObject = media;
            this.options.input.play();

            const servers = null;

            let local = new RTCPeerConnection(servers),
                remote = new RTCPeerConnection(servers);

            local.onicecandidate = e => {
                remote.addIceCandidate(e.candidate);
            };

            remote.onicecandidate = e => {
                local.addIceCandidate(e.candidate);
            };

            remote.ontrack = e => {
                if (this.options.output.srcObject !== e.streams[0]) {
                    this.options.output.srcObject = e.streams[0];
                }
            };

            for (let track of media.getTracks()) {
                local.addTrack(track, media);
            }

            local.createOffer(
                description => {
                    local.setLocalDescription(description);
                    remote.setRemoteDescription(description);
                    remote.createAnswer(description => {
                        remote.setLocalDescription(description);
                        local.setRemoteDescription(description);
                    });
                },

                () => {},

                {
                    offerToReceiveAudio: 1,
                    offerToReceiveVideo: 1
                }
            );

            console.log(local);
        });
    }

    stop() {
        //
    }

    pause() {
        //
    }
}

export default Broadcaster
