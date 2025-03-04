const express = require("express");
const bodyParser = require("body-parser");
const crypto = require("crypto");

const app = express();
const PORT = process.env.PORT || 3000;

app.use(bodyParser.json());

// Webhook endpoint
app.post("/webhook", (req, res) => {
    const secret = "YOUR_SECRET_KEY"; // Replace with your Paystack Secret Key
    const hash = crypto.createHmac("sha512", secret).update(JSON.stringify(req.body)).digest("hex");

    if (hash !== req.headers["x-paystack-signature"]) {
        return res.status(400).send("Invalid signature");
    }

    const event = req.body;
    
    if (event.event === "charge.success") {
        const reference = event.data.reference;
        const amount = event.data.amount / 100; // Convert kobo to NGN
        const customerEmail = event.data.customer.email;
        
        console.log(`Payment verified: Reference - ${reference}, Amount - ${amount}, Email - ${customerEmail}`);
        
        // Save this to a database or log file (for later retrieval)
    }

    res.sendStatus(200);
});

app.listen(PORT, () => {
    console.log(`Webhook server running on port ${PORT}`);
});
