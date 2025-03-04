const express = require("express");
const fetch = require("node-fetch");
const cors = require("cors");

const app = express();
app.use(cors());

const PAYSTACK_SECRET_KEY = "sk_live_xxxx"; // Replace with your Paystack Secret Key

app.get("/verify-payment", async (req, res) => {
    const reference = req.query.reference;
    
    if (!reference) {
        return res.status(400).json({ success: false, message: "Reference is required" });
    }

    try {
        let response = await fetch(`https://api.paystack.co/transaction/verify/${reference}`, {
            method: "GET",
            headers: {
                "Authorization": `Bearer ${PAYSTACK_SECRET_KEY}`,
                "Content-Type": "application/json"
            }
        });

        let data = await response.json();

        if (data.status && data.data.status === "success") {
            return res.json({ success: true, ticket: "ARENA-" + Math.floor(10000 + Math.random() * 90000) });
        } else {
            return res.json({ success: false, message: "Payment not verified" });
        }
    } catch (error) {
        return res.json({ success: false, message: "Error verifying payment" });
    }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log(`Server running on port ${PORT}`));
