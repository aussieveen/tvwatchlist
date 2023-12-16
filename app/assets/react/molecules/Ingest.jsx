import React from "react";
import Collapsible from 'react-collapsible';
import { useState, useEffect } from "react";
import ShowPoster from "../atoms/ShowPoster";
import IngestForm from "../organisms/IngestForm";

export default function Ingest() {
    const [showData, setShowData ] = useState(null);
    const [error, setError] = useState(null);
    const [searching, setSearching] = useState(false);
    const [inputValue, setInputValue] = useState('')
    const [timer, setTimer] = useState(0)

    const handleChange = event => {
        setInputValue(event.target.value)
        clearTimeout(timer)

        const newTimer = setTimeout(() => {
            if(event.target.value === '') return
            searchShows(event.target.value)
        }, 500)

        setTimer(newTimer)
    }
    function searchShows(string) {
        console.log("searching for " + string)
        setSearching(true);
        fetch(`/api/tvdb/search?showTitle=`+string+`&type=series`, {
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
        .then((showData) => {
            setShowData(showData);
        })
        .catch((err) => {
            setError(err.message);
            setShowData(null);
        })
        .finally(() => {
            setSearching(false);
        });
    }

    useEffect(() => {}, []);

    return (
        <div>
            {error && (
                <div>{`There is a problem fetching the post data - ${error}`}</div>
            )}
            <div>
                <h1>Ingest TV series</h1>
                <input value={inputValue} name="search" onChange={handleChange} placeholder="Search for tv show to ingest"/>
            </div>
            {searching &&
                <div>Searching...</div>
            }
            {showData && showData.data.map((show) => (
                <div key={show.id} className="ingestCard">
                    <h3>{show.title}</h3>
                    <ShowPoster
                        image={show.poster}
                        title={show.title}
                    />
                    <p>{show.overview}</p>
                    <Collapsible trigger="Ingest">
                        <IngestForm
                            id={show.id}
                        />
                    </Collapsible>
                </div>
            ))}
            <div>

            </div>
        </div>
    )
}