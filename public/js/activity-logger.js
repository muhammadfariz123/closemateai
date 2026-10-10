window.logSysActivity = function(type, actor, title, desc, icon, color) {
    // Save to backend DB
    fetch('/api/activities', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ type, actor, title, desc, icon, color })
    }).catch(e => console.error('Failed to log activity:', e));

    // Keep localStorage for backward compatibility with activity.blade.php if it hasn't been refreshed
    let acts = JSON.parse(localStorage.getItem('sys_activities')) || [];
    const now = new Date();
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const month = months[now.getMonth()];
    const timeStr = `${now.getDate().toString().padStart(2, '0')} ${month}, ${now.getHours().toString().padStart(2, '0')}.${now.getMinutes().toString().padStart(2, '0')}`;
    acts.unshift({ type, actor, title, desc, time: timeStr, icon, color });
    localStorage.setItem('sys_activities', JSON.stringify(acts));
};
