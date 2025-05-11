import {
    BrowserRouter as Router,
    Routes,
    Route,
    Navigate,
} from "react-router-dom";
import AdminLogin from "./pages/admin/Login";
import ProtectedRoute from "./components/ProtectedRoute";
import AdminDashboard from "./pages/admin/Dashboard";

function App() {
    return (
        <div>
            <h1>Hello World</h1>
        </div>
    );
}

export default App;
