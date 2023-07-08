import React from 'react'
import Moment from 'moment'

export default function Episode({title, description, season, episode, airDate}) {
    return (
        <div className= "component" id = "episode">
            <div id="episodeHeader">
                <p id = "episodeDetails">Season: {season} Episode: {episode}<br/>{Moment(airDate).format('Do MMM YYYY')}</p>
                <h4>{title}</h4>
            </div>
            <p>{description}</p>
        </div>
    )
}