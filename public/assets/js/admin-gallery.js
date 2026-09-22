let selectedFiles = [];
let isUploading = false;

const UPLOAD_BATCH_SIZE = 10;

$(document).ready(function () {
    $(document).on("change", ".photo-uploader__file-input", function () {
        const newFiles = Array.from(this.files);
        selectedFiles.push(...newFiles);
        renderSelectedFiles();
        this.value = "";
    });

    $(document).on("click", "#submitUpload", async function () {
        if (isUploading || selectedFiles.length === 0) {
            return;
        }

        const $uploadButton = $(this);
        const filesToUpload = [...selectedFiles];
        isUploading = true;
        $uploadButton.prop("disabled", true).text("Uploading...");

        try {
            for (
                let start = 0;
                start < filesToUpload.length;
                start += UPLOAD_BATCH_SIZE
            ) {
                const batch = filesToUpload.slice(
                    start,
                    start + UPLOAD_BATCH_SIZE,
                );
                const formData = new FormData();

                batch.forEach((file) => formData.append("images[]", file));
                formData.append("galleryType", GALLERY_TYPE);

                const response = await fetch("/admin/upload", {
                    method: "POST",
                    body: formData,
                });
                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || "Upload failed");
                }

                data.images.forEach(appendUploadedImage);
                selectedFiles.splice(0, batch.length);
                renderSelectedFiles();
            }

            $(".selected-files__list").empty();
        } catch (error) {
            console.error(error);
            window.alert(error.message || "Upload failed");
        } finally {
            isUploading = false;
            $uploadButton.prop("disabled", false).text("Upload");
        }
    });

    $(document).on("click", ".file-item__remove-btn", function () {
        const index = $(this).closest(".file-item").data("index");

        selectedFiles.splice(index, 1);

        renderSelectedFiles();
    });

    $(document).on("click", "#save-order", async function () {
        // get list make a sort
        let positions = [];
        $("#photos li").each(function (index) {
            positions.push({
                id: $(this).data("pg-id"),
                position: index + 1,
            });
        });

        const formData = new FormData();
        console.log(positions);
        formData.append("positions", JSON.stringify(positions));
        formData.append("galleryType", GALLERY_TYPE);

        try {
            const response = await fetch("/admin/reorder", {
                method: "POST",
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error("Upload failed");
            }

            //const data = await response.json();

            console.log(data);

            selectedFiles = [];
        } catch (error) {
            console.error(error);
        } finally {
            // clear input
            isUploading = false;
        }

        console.log(positions);
    });

    $(document).on("click", ".photo__delete-btn", async function () {
        const imgId = $(this).closest("li").attr("data-pg-id");

        try {
            const response = await fetch("/admin/delete/" + imgId, {
                method: "POST",
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error("Upload failed");
            }
            //const data = await response.json();
            $(this).closest("li").remove();

            // selectedFiles = [];
        } catch (error) {
            console.error(error);
        } finally {
            // clear input
            isUploading = false;
        }
    });

    $(document).ready(function () {
        const $photos = $("#photos");
        const $result = $("#result");

        if (!$photos.length || typeof Sortable === "undefined") {
            return;
        }

        const getPhotoOrder = () =>
            $photos
                .find(".photo")
                .map((_, photo) => $(photo).data("pgId"))
                .get();

        const updateResult = () => {
            if ($result.length) {
                $result.text(
                    `Aktualna kolejność: ${getPhotoOrder().join(", ")}`,
                );
            }
        };

        new Sortable($photos[0], {
            animation: 180,
            chosenClass: "sortable-chosen",
            dragClass: "sortable-drag",
            ghostClass: "sortable-ghost",
            draggable: ".photo",
            filter: ".photo__actions, .photo__delete-btn",
            preventOnFilter: true,
            onEnd: () => {
                updateResult();
            },
        });

        updateResult();
    });

    const $photos = $("#photos");
    const $result = $("#result");
    let $draggedPhoto = null;
    let $dropPlaceholder = null;

    if (!$photos.length || typeof Sortable !== "undefined") {
        return;
    }

    const getPhotoOrder = () =>
        $photos
            .find(".photo")
            .map((_, photo) => $(photo).data("pgId"))
            .get();

    const updateResult = () => {
        if ($result.length) {
            $result.text(`Aktualna kolejność: ${getPhotoOrder().join(", ")}`);
        }
    };

    $photos.on("dragstart", ".photo", function (event) {
        $draggedPhoto = $(this);
        $dropPlaceholder = $("<li>", {
            class: "drag-placeholder",
            css: { height: `${$draggedPhoto.outerHeight()}px` },
        });

        $draggedPhoto.after($dropPlaceholder);
        $draggedPhoto.addClass("is-dragging");

        const nativeEvent = event.originalEvent;
        nativeEvent.dataTransfer.effectAllowed = "move";
        nativeEvent.dataTransfer.setData(
            "text/plain",
            $draggedPhoto.data("pgId"),
        );
    });

    $photos.on("dragover", ".photo", function (event) {
        event.preventDefault();
        const $photo = $(this);

        if (!$photo.is($draggedPhoto)) {
            $photo.addClass("is-drag-over");

            const targetOffset = $photo.offset();
            const mouseClientY = event.originalEvent.clientY;
            const elementTop = targetOffset.top - $(window).scrollTop();
            const insertAfter =
                mouseClientY > elementTop + $photo.outerHeight() / 2;

            if ($dropPlaceholder) {
                if (insertAfter) {
                    $photo.after($dropPlaceholder);
                } else {
                    $photo.before($dropPlaceholder);
                }
            }
        }
    });

    $photos.on("dragleave", ".photo", function () {
        $(this).removeClass("is-drag-over");
    });

    $photos.on("drop", ".photo", function (event) {
        event.preventDefault();
        const $photo = $(this);
        $photo.removeClass("is-drag-over");

        if (!$draggedPhoto || $draggedPhoto.is($photo)) {
            return;
        }

        if ($dropPlaceholder) {
            $dropPlaceholder.replaceWith($draggedPhoto);
            $dropPlaceholder = null;
        }

        updateResult();
    });

    $photos.on("dragend", ".photo", function () {
        if ($draggedPhoto) {
            $draggedPhoto.removeClass("is-dragging");
        }
        $photos.find(".photo").removeClass("is-drag-over");

        if ($dropPlaceholder) {
            $dropPlaceholder.remove();
            $dropPlaceholder = null;
        }
        $draggedPhoto = null;
    });

    updateResult();
});

function renderSelectedFiles() {
    const $list = $(".selected-files__list");

    $list.empty();

    selectedFiles.forEach((file, index) => {
        $list.append(prepareUploadLi(file, index));
    });
}

function prepareUploadLi(file, index) {
    const fileUrl = URL.createObjectURL(file);

    return $(`
            <li class="file-item" data-index="${index}">
                <div class="file-item__preview">
                    <img src="${fileUrl}" alt="${file.name}">
                </div>

                <div class="file-item__details">
                    <span class="file-item__name">${file.name}</span>
                    <span class="file-item__size">
                        ${formatFileSize(file.size)}
                    </span>
                </div>

                <button
                    type="button"
                    class="file-item__remove-btn"
                    title="Usuń plik"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </li>
        `);
}

function formatFileSize(bytes) {
    if (bytes === 0) {
        return "0 B";
    }

    const units = ["B", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));

    return `${(bytes / Math.pow(1024, i)).toFixed(1)} ${units[i]}`;
}

function appendUploadedImage(image) {
    const photos = document.querySelector("#photos");
    if (!photos) {
        return;
    }

    const photo = document.createElement("li");
    photo.className = "photo";
    photo.dataset.pgId = image.pictureGalleryId;
    photo.draggable = true;
    photo.innerHTML = `
                <span class="drag-handle">☰</span>
                <img src="${GALLERY_PATH}/${encodeURIComponent(image.filename)}" alt="">
                <div>
                    <div class="photo-name"></div>
                </div>
                <div class="photo__actions">
                    <button type="button" class="photo__delete-btn" title="Usuń zdjęcie" aria-label="Usuń zdjęcie">
                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                    </button>
                </div>
            `;
    photo.querySelector(".photo-name").textContent = image.originalName;
    photos.appendChild(photo);
}
