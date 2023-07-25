import React from 'react'

export default function WatchedButton({id, refreshState}) {
    const handleClick = () => {
        fetch('http://localhost:10000/api/episodes/' + id, {
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
            console.log(response);
        })
        .finally(() => {
            console.log("Finally");
            {refreshState(id + 1)}
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