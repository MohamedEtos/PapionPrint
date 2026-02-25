document.addEventListener("DOMContentLoaded",function(){const a=document.getElementById("notification-list");if(a&&a.dataset.url){const s=a.dataset.url;setInterval(function(){fetch(s).then(e=>e.json()).then(e=>{const o=document.getElementById("notification-badge");o&&(o.innerText=e.unread_count);const n=document.getElementById("notification-header-count");if(n&&(n.innerText=e.unread_count+" New"),a){let i="";e.notifications.length>0?e.notifications.forEach(t=>{const l=t.status==="unread"?"primary":"";let c="";t.image_url?c=`<img src="${t.image_url}" alt="avatar" height="40" width="40">`:c='<i class="feather icon-plus-square font-medium-5 primary"></i>',i+=`
                                <a class="d-flex justify-content-between" href="javascript:void(0)">
                                    <div class="media d-flex align-items-start ">
                                        <div class="media-left">
                                            ${c}
                                        </div>
                                        <div class="media-body">
                                            <h6 class="${l} media-heading">${t.title??"-"}</h6>
                                            <small class="notification-text">${t.body??"-"}</small>
                                        </div><small>
                                            <time class="media-meta" datetime="${t.created_at}">${t.time_ago}</time></small>
                                    </div>
                                </a>`}):i='<div class="p-2 text-center text-muted">No New Notifications</div>',a.innerHTML=i}}).catch(e=>console.error("Error fetching notifications:",e))},1e4)}const r=document.getElementById("mark-all-read");r&&r.addEventListener("click",function(s){s.preventDefault();const e=this.dataset.url,o=document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");if(!o){console.error("CSRF token not found");return}fetch(e,{method:"POST",headers:{"X-CSRF-TOKEN":o,"Content-Type":"application/json",Accept:"application/json"}}).then(n=>n.json()).then(n=>{if(n.success){const i=document.getElementById("notification-badge");i&&(i.innerText="0");const t=document.getElementById("notification-header-count");t&&(t.innerText="0 New"),document.querySelectorAll("#notification-list .media-heading.primary").forEach(c=>{c.classList.remove("primary")})}}).catch(n=>console.error("Error marking all as read:",n))})});
