import React from 'react'

export default function WatchedButton({id, refreshState}) {
    const delay = (ms = 1000) => new Promise(r => setTimeout(r, ms));
    const handleClick = () => {
        const watchedEpisode = fetch('http://localhost:10000/api/episodes/' + id, {
            method: "PATCH",
            headers: {
                "Content-Type": "application/merge-patch+json"
            },
            body: JSON.stringify({
                watched: true
            })
        })
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        });

        watchedEpisode.then(episode => {
            const dateObject = new Date();
            let date = dateObject.toUTCString();
            return fetch('http://localhost:10000/api/histories', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    showTitle: episode.showTitle,
                    episodeTitle: episode.title,
                    airDate: episode.airDate,
                    watchedAt: date
                })
            });
        }).finally(() => {
            setTimeout(() => {refreshState()}, 1500);
        });
    };

    return (
        <div className="component text-center" id="watched">
            <button className="btn btn-lg btn-block btn-success" type="button" onClick={handleClick}>
                Watched
            </button>
        </div>
    )
}
