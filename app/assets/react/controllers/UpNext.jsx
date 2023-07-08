import React from 'react';
import { useState, useEffect } from "react";
import Episode from "../components/Episode";
import Show from "../components/Show";
import WatchedButton from "../components/WatchedButton";
import ShowPoster from "../components/ShowPoster";

export default function UpNext() {
    const [episodeData, setEpisodeData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    function refreshState(id = 1) {
        fetch(`http://localhost:10000/api/episodes/` + id, {
            method: "GET",
            headers: {
                "Content-Type": "application/json+ld"
            }
        })
            .then((response) => {
                if(!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((episodeData) => {
                setEpisodeData(episodeData);
                setError(null);
            })
            .catch((err) => {
                setError(err.message);
                setEpisodeData(null);
            })
            .finally(() => {
                setLoading(false);
            });
    }

    useEffect(() => { refreshState(); }, []);

    return (
        <div>
            {loading && <div>Loading...</div>}
            {error && (
                <div>{`There is a problem fetching the post data - ${error}`}</div>
            )}
            {episodeData &&
                (<div key={episodeData.id}>
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
                </div>)
            }
        </div>
    )
}