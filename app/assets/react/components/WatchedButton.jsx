import React from 'react'

export default function WatchedButton({id, refreshState}) {
    const handleClick = () => {
        console.log("Watched button clicked. ID: " + id);
        id = id + 1;
        {refreshState(id)};
    };

    return (
        <div className="component text-center" id="watched">
            <button class="btn btn-lg btn-block btn-success" type="button" onClick={handleClick}>
                Watched
            </button>
        </div>
    )
}