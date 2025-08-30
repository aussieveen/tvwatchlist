import React from 'react'

export default function WatchedButton({id, refreshState}) {
    const handleClick = () => {
        const watchedEpisode = fetch('/api/episode/' + id + '/watched', {
            method: "POST"
        })
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
        }).finally(() => {
            refreshState();
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
