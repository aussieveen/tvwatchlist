import React from 'react'

export default function Show({title, description}) {
    return (
        <div className= "component text-center" id="show">
            <h2>{title}</h2>
            {/*<p id="showDescription">{description}</p>*/}
        </div>
    )
}