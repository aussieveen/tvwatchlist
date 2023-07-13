import React from 'react'

export default function WatchedButton({id, refreshState}) {
    const handleClick = () => {
        fetch('http://localhost:10000/api/episodes/' + id, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json+ld"
            },
            body: JSON.stringify({
                watched: true
            })
        })
        .finally(() => {
            {refreshState()}
        });
    };

    return (
        <div className="component text-center" id="watched">
            <button class="btn btn-lg btn-block btn-success" type="button" onClick={handleClick}>
                Watched
            </button>
        </div>
    )
}