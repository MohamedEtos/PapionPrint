document.addEventListener('DOMContentLoaded', function () {
    const notificationList = document.getElementById('notification-list');

    // Check if element exists and has the URL
    if (notificationList && notificationList.dataset.url) {
        const url = notificationList.dataset.url;

        setInterval(function () {
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Update Badge
                    const badge = document.getElementById('notification-badge');
                    if (badge) badge.innerText = data.unread_count;

                    // Update Header
                    const headerCount = document.getElementById('notification-header-count');
                    if (headerCount) headerCount.innerText = data.unread_count + ' New';

                    // Update List
                    if (notificationList) {
                        let html = '';
                        if (data.notifications.length > 0) {
                            data.notifications.forEach(notify => {
                                const activeClass = notify.status === 'unread' ? 'primary' : '';
                                // const bgClass = notify.status === 'unread' ? 'bg-light' : '';

                                let imgHtml = '';
                                if (notify.image_url) {
                                    imgHtml = `<img src="${notify.image_url}" alt="avatar" height="40" width="40">`;
                                } else {
                                    imgHtml = `<i class="feather icon-plus-square font-medium-5 primary"></i>`;
                                }

                                html += `
                                <a class="d-flex justify-content-between" href="javascript:void(0)">
                                    <div class="media d-flex align-items-start ">
                                        <div class="media-left">
                                            ${imgHtml}
                                        </div>
                                        <div class="media-body">
                                            <h6 class="${activeClass} media-heading">${notify.title ?? '-'}</h6>
                                            <small class="notification-text">${notify.body ?? '-'}</small>
                                        </div><small>
                                            <time class="media-meta" datetime="${notify.created_at}">${notify.time_ago}</time></small>
                                    </div>
                                </a>`;
                            });
                        } else {
                            html = '<div class="p-2 text-center text-muted">No New Notifications</div>';
                        }
                        notificationList.innerHTML = html;
                    }
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }, 10000); // 10 seconds
    }

    // Mark All Read Logic
    const markAllReadBtn = document.getElementById('mark-all-read');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.dataset.url;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (!csrfToken) {
                console.error('CSRF token not found');
                return;
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reset Badge
                    const badge = document.getElementById('notification-badge');
                    if (badge) badge.innerText = '0';
                    
                    // Reset Header
                    const headerCount = document.getElementById('notification-header-count');
                    if (headerCount) headerCount.innerText = '0 New';

                    // Visually mark current list as read
                    const listItems = document.querySelectorAll('#notification-list .media-heading.primary');
                    listItems.forEach(el => {
                        el.classList.remove('primary');
                    });
                }
            })
            .catch(error => console.error('Error marking all as read:', error));
        });
    }
});
