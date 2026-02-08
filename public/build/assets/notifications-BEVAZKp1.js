document.addEventListener("DOMContentLoaded",function(){const i=document.getElementById("notification-list");if(i&&i.dataset.url){const l=i.dataset.url;setInterval(function(){fetch(l).then(t=>t.json()).then(t=>{const s=document.getElementById("notification-badge");s&&(s.innerText=t.unread_count);const c=document.getElementById("notification-header-count");if(c&&(c.innerText=t.unread_count+" New"),i){let a="";t.notifications.length>0?t.notifications.forEach(e=>{const o=e.status==="unread"?"primary":"";let n="";e.image_url?n=`<img src="${e.image_url}" alt="avatar" height="40" width="40">`:n='<i class="feather icon-plus-square font-medium-5 primary"></i>',a+=`
                                <a class="d-flex justify-content-between" href="javascript:void(0)">
                                    <div class="media d-flex align-items-start ">
                                        <div class="media-left">
                                            ${n}
                                        </div>
                                        <div class="media-body">
                                            <h6 class="${o} media-heading">${e.title??"-"}</h6>
                                            <small class="notification-text">${e.body??"-"}</small>
                                        </div><small>
                                            <time class="media-meta" datetime="${e.created_at}">${e.time_ago}</time></small>
                                    </div>
                                </a>`}):a='<div class="p-2 text-center text-muted">No New Notifications</div>',i.innerHTML=a}}).catch(t=>console.error("Error fetching notifications:",t))},1e4)}});
