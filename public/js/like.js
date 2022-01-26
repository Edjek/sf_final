document.querySelectorAll('a.js-likes').forEach(function (link) {
    link.addEventListener('click', onClickLike);
})
document.querySelectorAll('a.js-dislikes').forEach(function (link) {
    link.addEventListener('click', onClickDislike);
})

function onClickLike(event) {
    event.preventDefault();
    const url = this.href;
    const spanCount = this.querySelector('span.js-likes');
    const spanCountDislike = document.querySelector('span.js-dislikes');
    const icone = this.querySelector('i');
    const iconeDislike = document.querySelector('i.dislike');
    axios.get(url).then(function (response) {
        spanCount.textContent = response.data.likes;
        spanCountDislike.textContent = response.data.dislikes;
        if (icone.classList.contains('bi-hand-thumbs-up-fill')) {
            icone.classList.replace('bi-hand-thumbs-up-fill', 'bi-hand-thumbs-up')
        } else {
            icone.classList.replace('bi-hand-thumbs-up', 'bi-hand-thumbs-up-fill')
            iconeDislike.classList.replace('bi-hand-thumbs-down-fill', 'bi-hand-thumbs-down')
        }
    }).catch(function (error) {
        if (error.response.status === 403) {
            window.alert("Vous devez vous connecter")
        } else {
            window.alert("Une erreur s'est produite")
        }
    })
}

function onClickDislike(event) {
    event.preventDefault();
    const url = this.href;
    const spanCount = this.querySelector('span.js-dislikes');
    const spanCountLike = document.querySelector('span.js-likes');
    const icone = this.querySelector('i');
    const iconeLike = document.querySelector('i.like');
    axios.get(url).then(function (response) {
        spanCount.textContent = response.data.dislikes;
        spanCountLike.textContent = response.data.likes;
        if (icone.classList.contains('bi-hand-thumbs-down-fill')) {
            icone.classList.replace('bi-hand-thumbs-down-fill', 'bi-hand-thumbs-down')
        } else {
            icone.classList.replace('bi-hand-thumbs-down', 'bi-hand-thumbs-down-fill')
            iconeLike.classList.replace('bi-hand-thumbs-up-fill', 'bi-hand-thumbs-up')
        }
    }).catch(function (error) {
        if (error.response.status === 403) {
            window.alert("Vous devez vous connecter")
        } else {
            window.alert("Une erreur s'est produite")
        }
    })
}