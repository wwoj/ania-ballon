$(document).ready(function () {
    const $modal = $("#photo-modal");
    const $modalImage = $modal.find(".modal-gallery__image");
    const $modalCaption = $modal.find(".modal-gallery__caption");

    let galleryItems = [];
    let currentIndex = 0;

    let touchStartX = 0;
    let touchEndX = 0;

    const updateModalContent = () => {
        if (galleryItems.length === 0) return;

        const currentItem = galleryItems[currentIndex];
        $modalImage
            .attr("src", currentItem.src)
            .attr("alt", currentItem.caption);
        $modalCaption.text(currentItem.caption);
    };

    const openModal = (index) => {
        currentIndex = index;
        updateModalContent();

        $modal.addClass("modal-gallery--is-open");
        $("body").css("overflow", "hidden");
    };

    const closeModal = () => {
        $modal.removeClass("modal-gallery--is-open");
        $("body").css("overflow", "");

        setTimeout(() => {
            $modalImage.attr("src", "").attr("alt", "");
            $modalCaption.text("");
        }, 300);
    };

    const prevImage = () => {
        if (galleryItems.length <= 1) return;
        currentIndex =
            (currentIndex - 1 + galleryItems.length) % galleryItems.length;
        updateModalContent();
    };

    const nextImage = () => {
        if (galleryItems.length <= 1) return;
        currentIndex = (currentIndex + 1) % galleryItems.length;
        updateModalContent();
    };

    $(document).on("click", "img.grid-item__image", function (e) {
        const $images = $("img.grid-item__image");
        galleryItems = [];

        $images.each(function (i) {
            const $img = $(this);
            const src = $img.attr("src");
            const caption = $img.attr("alt") || "";

            galleryItems.push({ src, caption });

            if (this === e.target) {
                currentIndex = i;
            }
        });

        openModal(currentIndex);
    });

    $modal.on("click", ".modal-gallery__nav--prev", function (e) {
        e.stopPropagation();
        prevImage();
    });

    $modal.on("click", ".modal-gallery__nav--next", function (e) {
        e.stopPropagation();
        nextImage();
    });

    $modal.on("touchstart", function (e) {
        touchStartX = e.originalEvent.touches[0].clientX;
    });

    $modal.on("touchend", function (e) {
        touchEndX = e.originalEvent.changedTouches[0].clientX;
        handleSwipe();
    });

    const handleSwipe = () => {
        const swipeDistance = touchEndX - touchStartX;
        const minSwipeDistance = 40;

        if (swipeDistance < -minSwipeDistance) {
            nextImage();
        } else if (swipeDistance > minSwipeDistance) {
            prevImage();
        }
    };

    $modal.on(
        "click",
        ".modal-gallery__close-btn, .modal-gallery__backdrop",
        function () {
            closeModal();
        },
    );

    $(document).on("keydown", function (e) {
        if (!$modal.hasClass("modal-gallery--is-open")) return;

        if (e.key === "Escape") closeModal();
        if (e.key === "ArrowLeft") prevImage();
        if (e.key === "ArrowRight") nextImage();
    });
});
