let icons = document.querySelector('.col--icons ul.icons');
let icon = icons.querySelectorAll('img');

function downFn(url, name) {
    const pattern = /^(ftp|http|https):\/\/[^ "]+$/;
    if (!pattern.test(url)) {
        console.log("Invalid URL");
        return;
    }
    fetch(url)
        .then((res) => {
            if (!res.ok) {
                throw new Error("Network Problem");
            }
            return res.blob();
        })
        .then((file) => {
            let tUrl = URL.createObjectURL(file);
            const tmp1 = document.createElement("a");
            tmp1.href = tUrl;
            tmp1.download = name;
            document.body.appendChild(tmp1);
            tmp1.click();
            URL.revokeObjectURL(tUrl);
            tmp1.remove();
        })
        .catch(() => {
            console.log("Error");
        });
}


for (let index = 0; index < icon.length; index++) {
    try {
        // get data data-png
        let dataPng = icon[index].getAttribute('src');
        let dataName = icon[index].getAttribute('title');
        dataName = dataName.toLowerCase();
        //remove icon form name
        dataName = dataName.replace(' icon', '');
        // Download icon
        if (dataPng && dataName) {
            downFn(dataPng, dataName + '.png');
            // console.log(dataPng);
        }
    } catch (error) {
        console.log(icon[index]);
    }
    await new Promise(resolve => setTimeout(resolve, 1000));
}
