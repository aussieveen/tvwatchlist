import React from 'react'

export default function Poster({image, title}) {
    return (
        <div className= "component" class="poster-container">
            <img src={image} class="img-fluid" alt={title} />
        </div>
    )
}