window.LiveStream = {
    ws: new WebSocket(`wss://${location.host}/one2many`),
    video: null,
    webRtcPeer: null,

    setVideo(video) {
        LiveStream.video = video;
    },

    presenter() {
        if (! LiveStream.webRtcPeer) {
            LiveStream.webRtcPeer = kurentoUtils.WebRtcPeer.WebRtcPeerSendonly({
                localVideo: LiveStream.video,
                onicecandidate: LiveStream.onIceCandidate
            }, function(error) {
                if (error) return console.error(error);
                this.generateOffer(LiveStream.onOfferPresenter);
            });
        }
    },

    viewer() {
        if (! LiveStream.webRtcPeer) {
            LiveStream.webRtcPeer = kurentoUtils.WebRtcPeer.WebRtcPeerRecvonly({
                remoteVideo: LiveStream.video,
                onicecandidate: LiveStream.onIceCandidate
            }, function(error) {
                if (error) return console.error(error);
                this.generateOffer(LiveStream.onOfferViewer);
            });
        }
    },

    listen() {
        LiveStream.ws.onmessage = message => {
            let m = JSON.parse(message.data);
            console.info(`Received message: ${message.data}`);

            switch (m.id) {
                case 'presenterResponse':
                    if (m.response != 'accepted') {
                        LiveStream.dispose(`Call not accepted: ${m.message}`);
                    } else {
                        LiveStream.webRtcPeer.processAnswer(m.sdpAnswer);
                    }
                    break;

                case 'viewerResponse':
                    if (m.response != 'accepted') {
                        LiveStream.dispose(`Call not accepted: ${m.message}`);
                    } else {
                        LiveStream.webRtcPeer.processAnswer(m.sdpAnswer);
                    }
                    break;

                case 'stopCommunication':
                    LiveStream.dispose();
                    break;

                case 'iceCandidate':
                    LiveStream.webRtcPeer.addIceCandidate(m.candidate)
                    break;

                default:
                    console.error('Unrecognized message', m);
            }
        }
    },

    dispose(message) {
        console.error(message);

        if (LiveStream.webRtcPeer) {
            LiveStream.webRtcPeer.dispose();
            LiveStream.webRtcPeer = null;
        }
    },

    onIceCandidate(candidate) {
        console.log('Local candidate', candidate);

        LiveStream.sendMessage({
            id: 'onIceCandidate',
            candidate: candidate
        });
    },

    onOfferPresenter(error, offerSdp) {
        if (error) return console.error(error);

        LiveStream.sendMessage({
            id: 'presenter',
            sdpOffer: offerSdp
        });
    },

    onOfferViewer(error, offerSdp) {
        if (error) return console.error(error);

        LiveStream.sendMessage({
            id: 'viewer',
            sdpOffer: offerSdp
        });
    },

    stop() {
        if (LiveStream.webRtcPeer) {
            LiveStream.sendMessage({
                id: 'stop'
            });

            LiveStream.dispose();
        }
    },

    sendMessage(message) {
        let jsonMessage = JSON.stringify(message);
        console.log('Senging message', jsonMessage);
        LiveStream.ws.send(jsonMessage);
    },

    unload() {
        LiveStream.ws.close();
    }
}
