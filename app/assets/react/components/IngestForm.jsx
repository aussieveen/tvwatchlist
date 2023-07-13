import React, {useState} from 'react'

export default function IngestForm({id}) {

    const [ingestState, setIngestState ] = useState("Start");
    const [ingestDisabled, setIngestDisabled ] = useState('');
    const [ingestSeason, setIngestSeason ] = useState(1);
    const [ingestEpisode, setIngestEpisode ] = useState(1);
    const [ingestPlatform, setIngestPlatform ] = useState("Plex");

    function ingestShow(id) {
        console.log("Ingesting show " + id + " on platform " + ingestPlatform + " season " + ingestSeason + " episode " + ingestEpisode);
        fetch('http://localhost:10000/api/tvdb/series/ingest',{
            method: "POST",
            headers: {
                "Content-Type": "application/json+ld"
            },
            body: JSON.stringify({
                seriesId: id,
                season: ingestSeason,
                episode: ingestEpisode,
                platform: ingestPlatform
            })
        })
        .then((response) => {
            if(!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((ingestData) => {
            console.log("Ingested show " + ingestData);
            setIngestState("Ingested");
            setIngestDisabled("disabled");
        })
        .catch((err) => {
            console.log("Error ingesting show " + err.message);
            setIngestState("Error during Ingest");
            setIngestDisabled("disabled");
        })
    }

    return (
        <div className="ingestForm">
            <div className="partialIngest">
                <div className="partialIngestInput">
                    <label htmlFor={id + "season"}>Season: </label>
                    <input name={id + "season"} type={"text"} placeholder={"1"} id={"season"} onChange={(e) => setIngestSeason(e.target.value)}></input>
                </div>
                <div className="partialIngestInput">
                    <label htmlFor={id + "season"}>Episode: </label>
                    <input name={id + "episode"} type={"text"} placeholder={"1"} id={"episode"} onChange={(e) => setIngestEpisode(e.target.value)}></input>
                </div>
            </div>
            <select className={"platformSelect"} onChange={(e) => setIngestPlatform(e.target.value)}>
                <option value="Plex">Plex</option>
                <option value="Netflix">Netflix</option>
                <option value="Disney Plus">Disney Plus</option>
                <option value="Amazon Prime">Amazon Prime</option>
            </select>
            <button className={"btn btn-lg btn-block btn-dark " + ingestDisabled} type="button" onClick={() => ingestShow(id)}>
                {ingestState}
            </button>
        </div>
    )
}