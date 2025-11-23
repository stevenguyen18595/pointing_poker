import React from "react";

interface CardProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
    value: string | number;
    isSelected: boolean;
    onClick: () => void;
}

const Card: React.FC<CardProps> = ({ value, isSelected, onClick }) => {
    return (
        <button
            onClick={onClick}
            className={`
                w-16 h-24 rounded-lg border-2 flex items-center justify-center font-bold text-lg
                transition-all duration-200 transform hover:scale-105
                ${
                    isSelected
                        ? "bg-blue-500 text-white border-blue-600 shadow-lg"
                        : "bg-white text-gray-700 border-gray-300 hover:border-blue-400"
                }
            `}
        >
            {value}
        </button>
    );
};

export default Card;
