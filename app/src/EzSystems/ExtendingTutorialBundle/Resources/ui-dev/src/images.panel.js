import React from 'react';
import ImagesList from './images.list';

const ImagesPanel = (props) => {
    const wrapperAttrs = {className: 'c-images-panel'};

    if (!props.isVisible) {
        wrapperAttrs.hidden = true;
    }

    return (
        <div {...wrapperAttrs}>
<h1>Images panel</h1>
    <ImagesList {...props} />
    </div>
);
};

export default ImagesPanel;