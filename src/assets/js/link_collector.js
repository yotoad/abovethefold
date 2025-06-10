document.addEventListener('DOMContentLoaded', function () {
    function isVisible(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.width > 0 &&
            rect.height > 0 &&
            rect.bottom > 0 &&
            rect.right > 0 &&
            rect.top < (window.innerHeight || document.documentElement.clientHeight) &&
            rect.left < (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    const links = Array.from(document.querySelectorAll('a')).filter(isVisible).map(link => ({
        href: link.href,
        text: link.textContent.trim()
    }));

    const payload = {
        screen: {
            width: window.innerWidth,
            height: window.innerHeight
        },
        links: links
    };

    // TODO: Replace with your endpoint URL when available
    fetch('/wp-json/abtf/v1/links', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });
});
