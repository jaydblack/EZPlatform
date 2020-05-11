import React, { Component } from 'react';
import Image from './image';
import { getImages } from './images.service';
import './css/images.list.css';

class ImagesList extends Component {
    constructor(props) {
        super(props);

        this.updateImagesState = this.updateImagesState.bind(this);
        this.showPrevPage = this.showPrevPage.bind(this);
        this.showNextPage = this.showNextPage.bind(this);
        this.state = {
            images: [],
            page: 0,
            itemsPerPage: 5,
            maxPageIndex: 0
        };
    }

    componentDidMount() {
        getImages(this.props.restInfo, this.updateImagesState);
    }

    updateImagesState(response) {
        const images = response.View.Result.searchHits.searchHit.map(item => item.value.Location);
        const perPage = this.state.itemsPerPage;
        const modulo = images.length % perPage;
        const maxPageIndex = modulo ? (images.length - modulo) / perPage : images.length / perPage - 1;

        this.setState({ images, maxPageIndex });
    }

    showPrevPage() {
        this.setState(state => Object.assign({}, state, {
            page: state.page > 0 ? state.page - 1 : 0
        }));
    }

    showNextPage() {
        this.setState(state => Object.assign({}, state, {
            page: state.maxPageIndex > state.page ? state.page + 1 : state.maxPageIndex
        }));
    }

    renderItems() {
        const attrs = {
            className: 'c-images-list__items',
            style: {
                transform: `translate3d(-${this.state.page * this.state.itemsPerPage * 316}px, 0, 0)`
            }
        };

        return (
            <div className="c-images-list__items-wrapper">
                <div {...attrs}>
                    {this.state.images.map(imageLocation => <Image key={imageLocation.id} location={imageLocation} restInfo={this.props.restInfo}/>)}
                </div>
            </div>
        );
    }

    renderPrevBtn() {
        const attrs = {
            className: 'c-images-list__btn--prev',
            onClick: this.showPrevPage
        };

        if (this.state.page <= 0) {
            attrs.disabled = true;
        }

        return (
            <div {...attrs}>
                <svg className="ez-icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#caret-back"></use>
                </svg>
            </div>
        );
    }

    renderNextBtn() {
        const attrs = {
            className: 'c-images-list__btn--next',
            onClick: this.showNextPage
        };

        if (this.state.page >= this.state.maxPageIndex) {
            attrs.disabled = true;
        }

        return (
            <div {...attrs}>
                <svg className="ez-icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#caret-next"></use>
                </svg>
            </div>
        );
    }

    render() {
        return (
            <div className="c-images-list">
                {this.renderPrevBtn()}
                {this.renderItems()}
                {this.renderNextBtn()}
            </div>
        );
    }
}

export default ImagesList;