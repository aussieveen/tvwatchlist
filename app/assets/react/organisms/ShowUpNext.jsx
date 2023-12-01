import React from 'react';
import Episode from "../atoms/Episode";
import Show from "../atoms/Show";
import WatchedButton from "../atoms/WatchedButton";
import ShowPoster from "../atoms/ShowPoster";

export default function ShowUpNext({episodeData, refreshState}) {
    return (
        <div key={episodeData.id}>
            <div className= "component" id="secondary">
                <Show
                    title={episodeData.showTitle}
                />
            </div>
            <div className= "component" id="primary">
                <ShowPoster
                    image={episodeData.poster}
                    title={episodeData.showTitle}
                />
                <Episode
                    airDate={episodeData.airDate}
                    title={episodeData.title}
                    description={episodeData.description}
                    episode={episodeData.episode}
                    season={episodeData.season}
                />
            </div>
            <WatchedButton id={episodeData.id} refreshState={refreshState}/>
        </div>
    )
}
